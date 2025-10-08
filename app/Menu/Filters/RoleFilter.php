<?php

namespace App\Menu\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Menu filter que permite usar el atributo `role` en las entradas del menú.
 * Si la entrada tiene 'role' y el usuario no lo tiene, marcará el item como restricted.
 */
class RoleFilter implements FilterInterface
{
    /**
     * Transform the menu item.
     *
     * @param array $item
     * @return array
     */
    public function transform($item)
    {
        // Si no hay atributo role, devolver tal cual
        if (!isset($item['role'])) {
            return $item;
        }

        $user = Auth::user();

        // Si no hay usuario autenticado, restringir
        if (!$user) {
            $item['restricted'] = true;
            return $item;
        }

        /** @var User $user */
        // Aseguramos con docblock que el analizador sepa que $user es App\Models\User (tiene hasRole)

        $roles = (array) $item['role'];

        // Permitir si el usuario tiene alguno de los roles requeridos
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $item;
            }
        }

        // Si llega aquí, no tiene el role
        $item['restricted'] = true;
        return $item;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para autenticación contra Oracle usando procedimientos almacenados
 *
 * Basado en UP_ADMSIS.PKG_VALIDA_USER
 */
class OracleAuthService
{
    /**
     * Constantes para estamentos
     */
    const ESTAMENTO_PROFESOR = 'P';
    const ESTAMENTO_ADMINISTRATIVO = 'A';
    const ESTAMENTO_ESTUDIANTE = 'E';

    /**
     * Validar credenciales de usuario contra Oracle
     *
     * @param string $estamento (P=Profesor, A=Administrativo, E=Estudiante)
     * @param string $provincia
     * @param string $clase
     * @param string $tomo
     * @param string $folio
     * @param string $password (ya hasheado con MD5)
     * @return bool
     */
    public function validateUser(string $estamento, string $provincia, string $clase, string $tomo, string $folio, string $password): bool
    {
        try {
            // Inicializar variable de respuesta
            $cresp = 0;

            // Llamar al procedimiento almacenado
            DB::statement("BEGIN UP_ADMSIS.PKG_VALIDA_USER.VALIDA_ESTAMENTO_USER(:estamento, :provincia, :clase, :tomo, :folio, :password, :cresp); END;", [
                'estamento' => $estamento,
                'provincia' => $provincia,
                'clase' => $clase,
                'tomo' => $tomo,
                'folio' => $folio,
                'password' => $password,
                'cresp' => &$cresp
            ]);

            return $cresp == 1;

        } catch (\Exception $e) {
            Log::error('Error validating user against Oracle', [
                'estamento' => $estamento,
                'provincia' => $provincia,
                'clase' => $clase,
                'tomo' => $tomo,
                'folio' => $folio,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Obtener detalles del usuario desde Oracle
     *
     * @param string $provincia
     * @param string $clase
     * @param string $tomo
     * @param string $folio
     * @param string $estamento
     * @return array|null
     */
    public function getUserDetails(string $provincia, string $clase, string $tomo, string $folio, string $estamento): ?array
    {
        try {
            $crespuesta = '';
            $userData = null;

            // Usar una consulta directa para obtener el cursor
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN UP_ADMSIS.PKG_VALIDA_USER.DETALLE_USUARIO_UP(:provincia, :clase, :tomo, :folio, :estamento, :crespuesta, :cursor); END;");

            // Crear cursor
            $cursor = $pdo->prepare("SELECT * FROM DUAL"); // Placeholder para el cursor
            $stmt->bindParam(':provincia', $provincia);
            $stmt->bindParam(':clase', $clase);
            $stmt->bindParam(':tomo', $tomo);
            $stmt->bindParam(':folio', $folio);
            $stmt->bindParam(':estamento', $estamento);
            $stmt->bindParam(':crespuesta', $crespuesta, \PDO::PARAM_STR, 200);
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);

            $stmt->execute();

            // Ejecutar el cursor
            $cursor->execute();

            // Obtener los datos
            $userData = $cursor->fetch(\PDO::FETCH_ASSOC);

            $stmt->closeCursor();
            $cursor->closeCursor();

            return $userData;

        } catch (\Exception $e) {
            Log::error('Error getting user details from Oracle', [
                'provincia' => $provincia,
                'clase' => $clase,
                'tomo' => $tomo,
                'folio' => $folio,
                'estamento' => $estamento,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Autenticar usuario completo (validar + obtener detalles)
     *
     * @param string $estamento
     * @param string $provincia
     * @param string $clase
     * @param string $tomo
     * @param string $folio
     * @param string $password
     * @return array|null Datos del usuario si válido, null si no
     */
    public function authenticateUser(string $estamento, string $provincia, string $clase, string $tomo, string $folio, string $password): ?array
    {
        // Primero validar credenciales
        $isValid = $this->validateUser($estamento, $provincia, $clase, $tomo, $folio, $password);

        if (!$isValid) {
            return null;
        }

        // Si válido, obtener detalles
        return $this->getUserDetails($provincia, $clase, $tomo, $folio, $estamento);
    }

    /**
     * Generar identificador único para el usuario basado en cédula
     *
     * @param string $provincia
     * @param string $clase
     * @param string $tomo
     * @param string $folio
     * @return string
     */
    public function generateUserIdentifier(string $provincia, string $clase, string $tomo, string $folio): string
    {
        return sprintf('%s-%s-%s-%s', $provincia, $clase, $tomo, $folio);
    }

    /**
     * Obtener nombre completo del estamento
     *
     * @param string $estamento
     * @return string
     */
    public function getEstamentoName(string $estamento): string
    {
        return match($estamento) {
            self::ESTAMENTO_PROFESOR => 'Profesor',
            self::ESTAMENTO_ADMINISTRATIVO => 'Administrativo',
            self::ESTAMENTO_ESTUDIANTE => 'Estudiante',
            default => 'Desconocido'
        };
    }
}

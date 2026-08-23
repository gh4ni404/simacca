<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * Base Service Class
 * 
 * Provides common functionality for all services:
 * - Database transaction management
 * - Validation handling
 * - Error handling and logging
 * - Response formatting
 */
abstract class BaseService
{
    /**
     * Database connection
     * 
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * Validation errors
     * 
     * @var array
     */
    protected $errors = [];

    /**
     * Logger instance
     * 
     * @var \CodeIgniter\Log\Logger
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->logger = service('logger');
    }

    /**
     * Begin database transaction
     * 
     * @return void
     */
    protected function beginTransaction(): void
    {
        $this->db->transStart();
    }

    /**
     * Commit database transaction
     * 
     * @return bool
     */
    protected function commitTransaction(): bool
    {
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Rollback database transaction
     * 
     * @return void
     */
    protected function rollbackTransaction(): void
    {
        $this->db->transRollback();
    }

    /**
     * Add validation error
     * 
     * @param string $field
     * @param string $message
     * @return void
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    /**
     * Get all validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if service has errors
     * 
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Clear all errors
     * 
     * @return void
     */
    protected function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Log message
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * Log info message
     * 
     * @param string $method
     * @param string $message
     * @return void
     */
    protected function logInfo(string $method, string $message): void
    {
        $this->logger->info(static::class . '::' . $method . ' - ' . $message);
    }

    /**
     * Log error message
     * 
     * @param string $method
     * @param \Exception|string $error
     * @return void
     */
    protected function logError(string $method, $error): void
    {
        if ($error instanceof \Exception) {
            $message = $error->getMessage();
            $this->logger->error(static::class . '::' . $method . ' - ' . $message, [
                'exception' => get_class($error),
                'file' => $error->getFile(),
                'line' => $error->getLine()
            ]);
        } else {
            $this->logger->error(static::class . '::' . $method . ' - ' . $error);
        }
    }

    /**
     * Log warning message
     * 
     * @param string $method
     * @param string $message
     * @return void
     */
    protected function logWarning(string $method, string $message): void
    {
        $this->logger->warning(static::class . '::' . $method . ' - ' . $message);
    }

    /**
     * Create success response
     * 
     * @param mixed $data
     * @param string $message
     * @return array
     */
    protected function successResponse($data = null, string $message = 'Success'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Create error response
     * 
     * @param string $message
     * @param int|array $codeOrErrors HTTP status code or array of errors
     * @param array $errors Additional errors (if code is provided)
     * @return array
     */
    protected function errorResponse(string $message, $codeOrErrors = 400, array $errors = []): array
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        // If second parameter is an integer, treat it as HTTP code
        if (is_int($codeOrErrors)) {
            $response['code'] = $codeOrErrors;
            $response['errors'] = !empty($errors) ? $errors : $this->errors;
        } 
        // Otherwise treat it as errors array (backward compatibility)
        else if (is_array($codeOrErrors)) {
            $response['errors'] = !empty($codeOrErrors) ? $codeOrErrors : $this->errors;
        }

        return $response;
    }

    /**
     * Short alias for successResponse()
     * 
     * @param mixed $data
     * @param string $message
     * @return array
     */
    protected function success($data = null, string $message = 'Success'): array
    {
        return $this->successResponse($data, $message);
    }

    /**
     * Short alias for errorResponse()
     * 
     * @param string $message
     * @param int $code
     * @return array
     */
    protected function error(string $message, int $code = 400): array
    {
        return $this->errorResponse($message, $code);
    }

    /**
     * Validate data using CodeIgniter validation
     * 
     * @param array $data
     * @param array $rules
     * @return bool
     */
    protected function validate(array $data, array $rules, array $messages = []): bool
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules($rules, $messages);
        
        if (!$validation->run($data)) {
            $this->errors = $validation->getErrors();
            return false;
        }
        
        return true;
    }

    /**
     * Execute callback within transaction
     * 
     * @param callable $callback
     * @return array
     */
    protected function executeInTransaction(callable $callback): array
    {
        $this->clearErrors();
        $this->beginTransaction();

        try {
            $result = $callback();
            
            if (!$this->commitTransaction()) {
                throw new \Exception('Transaction failed to complete');
            }

            return $this->successResponse($result);
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            $this->log('error', $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse($e->getMessage());
        }
    }
}

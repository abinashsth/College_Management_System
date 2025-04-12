<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception class for exam-related errors.
 * 
 * This class provides specialized exception handling for exam operations,
 * including scheduling conflicts, grading issues, and material management errors.
 */
class ExamException extends Exception
{
    /**
     * The error type code
     * 
     * @var string
     */
    protected $errorType;
    
    /**
     * Additional error context data
     * 
     * @var array
     */
    protected $context = [];
    
    /**
     * Exam exception error types
     */
    const TYPE_SCHEDULING_CONFLICT = 'scheduling_conflict';
    const TYPE_INVALID_GRADE = 'invalid_grade';
    const TYPE_MATERIAL_ERROR = 'material_error';
    const TYPE_PERMISSION_ERROR = 'permission_error';
    const TYPE_ENROLLMENT_ERROR = 'enrollment_error';
    const TYPE_SUPERVISION_ERROR = 'supervision_error';
    
    /**
     * Create a new exam exception instance.
     *
     * @param string $message
     * @param string $errorType
     * @param array $context
     * @param int $code
     * @param \Exception|null $previous
     * @return void
     */
    public function __construct(string $message, string $errorType = null, array $context = [], int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        
        $this->errorType = $errorType;
        $this->context = $context;
    }
    
    /**
     * Get the error type.
     *
     * @return string|null
     */
    public function getErrorType()
    {
        return $this->errorType;
    }
    
    /**
     * Get the error context data.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
    
    /**
     * Create a scheduling conflict exception.
     *
     * @param string $message
     * @param array $context
     * @return self
     */
    public static function schedulingConflict(string $message, array $context = [])
    {
        return new self(
            $message,
            self::TYPE_SCHEDULING_CONFLICT,
            $context,
            400
        );
    }
    
    /**
     * Create an invalid grade exception.
     *
     * @param string $message
     * @param array $context
     * @return self
     */
    public static function invalidGrade(string $message, array $context = [])
    {
        return new self(
            $message,
            self::TYPE_INVALID_GRADE,
            $context,
            400
        );
    }
    
    /**
     * Create a material error exception.
     *
     * @param string $message
     * @param array $context
     * @return self
     */
    public static function materialError(string $message, array $context = [])
    {
        return new self(
            $message,
            self::TYPE_MATERIAL_ERROR,
            $context,
            400
        );
    }
    
    /**
     * Create a permission error exception.
     *
     * @param string $message
     * @param array $context
     * @return self
     */
    public static function permissionError(string $message, array $context = [])
    {
        return new self(
            $message,
            self::TYPE_PERMISSION_ERROR,
            $context,
            403
        );
    }
    
    /**
     * Create an enrollment error exception.
     *
     * @param string $message
     * @param array $context
     * @return self
     */
    public static function enrollmentError(string $message, array $context = [])
    {
        return new self(
            $message,
            self::TYPE_ENROLLMENT_ERROR,
            $context,
            400
        );
    }
    
    /**
     * Create a supervision error exception.
     *
     * @param string $message
     * @param array $context
     * @return self
     */
    public static function supervisionError(string $message, array $context = [])
    {
        return new self(
            $message,
            self::TYPE_SUPERVISION_ERROR,
            $context,
            400
        );
    }
} 
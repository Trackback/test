<?php

namespace Core\Application\DataProcessing\Import\Source;

interface ImportSourceInterface
{
    /**
     * @return bool
     */
    public function isEmpty(): bool;
    
    /**
     * @return array
     */
    public function getOne(): array;
    
    /**
     * @return bool
     */
    public function hasNextRow(): bool;
}

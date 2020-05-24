<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

Trait ManageForeignKeys
{

    private $commands = [
        'mysql' => [
            'enable' => 'SET FOREIGN_KEY_CHECKS=1;',
            'disable' => 'SET FOREIGN_KEY_CHECKS=0;',
        ],
        'sqlite' => [
            'enable' => 'PRAGMA foreign_keys = ON;',
            'disable' => 'PRAGMA foreign_keys = OFF;'
        ]
    ];

    public function disable()
    {
        DB::statement($this->getDisableStatement());
    }

    public function enable()
    {
        DB::statement($this->getEnableStatement());
    }

    private function getEnableStatement()
    {
        return $this->getDriverCommands()['enable'];
    }

    private function getDisableStatement()
    {
        return $this->getDriverCommands()['disable'];
    }

    private function getDriverCommands()
    {
        return $this->commands[DB::getDefaultConnection()];
    }
}

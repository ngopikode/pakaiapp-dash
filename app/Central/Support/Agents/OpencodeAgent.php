<?php

declare(strict_types=1);

namespace App\Central\Support\Agents;

use Laravel\Boost\Contracts\SupportsGuidelines;
use Laravel\Boost\Contracts\SupportsMcp;
use Laravel\Boost\Contracts\SupportsSkills;
use Laravel\Boost\Install\Agents\Agent;

class OpencodeAgent extends Agent implements SupportsGuidelines, SupportsMcp, SupportsSkills
{
    /**
     * Get the path to the main guideline file.
     */
    public function guidelineFilePath(): string
    {
        return '.opencode/CLAUDE.md';
    }

    /**
     * Get the directory where skills should be installed.
     */
    public function skillsDirectory(): string
    {
        return '.opencode/skills';
    }

    /**
     * Get the path to the MCP configuration file.
     */
    public function mcpConfigPath(): string
    {
        return '.opencode/mcp.json';
    }
}

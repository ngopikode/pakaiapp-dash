<?php

declare(strict_types=1);

namespace App\Central\Support\Agents;

use Laravel\Boost\Contracts\SupportsGuidelines;
use Laravel\Boost\Contracts\SupportsMcp;
use Laravel\Boost\Contracts\SupportsSkills;
use Laravel\Boost\Install\Agents\Agent;
use Laravel\Boost\Install\Enums\Platform;

class OpencodeAgent extends Agent implements SupportsGuidelines, SupportsMcp, SupportsSkills
{
    /**
     * Get the path to the main guideline file.
     */
    public function guidelineFilePath(): string
    {
        return 'AGENTS.md';
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

    public function name(): string
    {
        return 'opencode';
    }

    public function displayName(): string
    {
        return 'Opencode';
    }

    public function systemDetectionConfig(Platform $platform): array
    {
        // For testing we will always return false here, project level matters
        return [];
    }

    public function projectDetectionConfig(): array
    {
        return [
            'paths' => [
                '.opencode',
            ],
        ];
    }

    public function guidelinesPath(): string
    {
        return 'AGENTS.md';
    }

    public function skillsPath(): string
    {
        return '.opencode/skills';
    }
}

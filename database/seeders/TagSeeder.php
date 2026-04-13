<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Áreas técnicas
            ['name' => 'Frontend',       'slug' => 'frontend'],
            ['name' => 'Backend',        'slug' => 'backend'],
            ['name' => 'Arquitectura',   'slug' => 'arquitectura'],
            ['name' => 'DevOps',         'slug' => 'devops'],
            ['name' => 'Seguridad',      'slug' => 'seguridad'],
            ['name' => 'Performance',    'slug' => 'performance'],
            // Tecnologías emergentes
            ['name' => 'IA',             'slug' => 'ia'],
            ['name' => 'Web3',           'slug' => 'web3'],
            ['name' => 'WebAssembly',    'slug' => 'webassembly'],
            ['name' => 'WebGL',          'slug' => 'webgl'],
            ['name' => 'Edge Computing', 'slug' => 'edge-computing'],
            // Lenguajes
            ['name' => 'JavaScript',     'slug' => 'javascript'],
            ['name' => 'TypeScript',     'slug' => 'typescript'],
            ['name' => 'Rust',           'slug' => 'rust'],
            ['name' => 'Python',         'slug' => 'python'],
            ['name' => 'Go',             'slug' => 'go'],
            // Frameworks y herramientas
            ['name' => 'Next.js',        'slug' => 'nextjs'],
            ['name' => 'React',          'slug' => 'react'],
            ['name' => 'Vue',            'slug' => 'vue'],
            ['name' => 'Docker',         'slug' => 'docker'],
            ['name' => 'Node.js',        'slug' => 'nodejs'],
            // Editorial
            ['name' => 'Tendencias',     'slug' => 'tendencias'],
            ['name' => 'Open Source',    'slug' => 'open-source'],
            ['name' => 'Industria',      'slug' => 'industria'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], ['name' => $tag['name']]);
        }
    }
}

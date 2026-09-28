import { spawn, spawnSync } from 'node:child_process';
import { closeSync, mkdirSync, openSync, rmSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { applicationEnvironment, baseURL, databasePath } from '../../playwright.config.js';

const serverPidPath = path.resolve('test-results/.server.pid');

function runArtisan(argumentsList) {
    const result = spawnSync('php', ['artisan', ...argumentsList], {
        cwd: path.resolve('.'),
        env: applicationEnvironment,
        encoding: 'utf8',
    });

    if (result.status !== 0) {
        throw new Error([
            `Falló php artisan ${argumentsList.join(' ')}.`,
            result.stdout,
            result.stderr,
        ].filter(Boolean).join('\n'));
    }
}

export default async function globalSetup() {
    mkdirSync(path.dirname(databasePath), { recursive: true });
    rmSync(databasePath, { force: true });
    closeSync(openSync(databasePath, 'w'));

    runArtisan(['migrate:fresh', '--force']);
    runArtisan(['db:seed', '--class=StatisticsVisualTestSeeder', '--force']);

    mkdirSync(path.dirname(serverPidPath), { recursive: true });
    const server = spawn('php', [
        '-S', '127.0.0.1:8011',
        '-t', 'public',
        'tests/Browser/server.php',
    ], {
        cwd: path.resolve('.'),
        env: applicationEnvironment,
        detached: true,
        stdio: 'ignore',
    });
    server.unref();
    writeFileSync(serverPidPath, String(server.pid));

    let lastError;
    for (let attempt = 0; attempt < 50; attempt += 1) {
        try {
            const response = await fetch(baseURL, { redirect: 'manual' });
            if (response.status > 0) return;
        } catch (error) {
            lastError = error;
        }
        await new Promise(resolve => setTimeout(resolve, 200));
    }

    throw new Error(`El servidor de pruebas no inició correctamente: ${lastError?.message || 'sin respuesta'}`);
}

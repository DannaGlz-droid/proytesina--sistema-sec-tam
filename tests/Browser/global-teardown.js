import { readFileSync, rmSync } from 'node:fs';
import path from 'node:path';

const serverPidPath = path.resolve('test-results/.server.pid');

export default async function globalTeardown() {
    try {
        const pid = Number(readFileSync(serverPidPath, 'utf8'));
        if (Number.isInteger(pid) && pid > 0) process.kill(pid);
    } catch (error) {
        if (error?.code !== 'ESRCH' && error?.code !== 'ENOENT') throw error;
    } finally {
        rmSync(serverPidPath, { force: true });
    }
}

import http from 'k6/http';
import { check, sleep } from 'k6';
import encoding from 'k6/encoding';

const baseUrl = (__ENV.BASE_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
const basicAuth = __ENV.BASIC_AUTH_USER && __ENV.BASIC_AUTH_PASSWORD
    ? `Basic ${encoding.b64encode(`${__ENV.BASIC_AUTH_USER}:${__ENV.BASIC_AUTH_PASSWORD}`)}`
    : null;
const requestOptions = basicAuth ? { headers: { Authorization: basicAuth } } : {};
const profile = __ENV.PROFILE || 'baseline';

const profiles = {
    smoke: [
        { duration: '10s', target: 1 },
        { duration: '20s', target: 1 },
        { duration: '5s', target: 0 },
    ],
    baseline: [
        { duration: '30s', target: 20 },
        { duration: '2m', target: 20 },
        { duration: '15s', target: 0 },
    ],
    growth: [
        { duration: '1m', target: 50 },
        { duration: '5m', target: 50 },
        { duration: '30s', target: 0 },
    ],
    stress: [
        { duration: '2m', target: 100 },
        { duration: '5m', target: 100 },
        { duration: '1m', target: 0 },
    ],
};

if (!profiles[profile]) {
    throw new Error(`Unknown load profile: ${profile}`);
}

if (/^https:\/\/(?:loja\.)?malu-store\.com(?:\/|$)/.test(`${baseUrl}/`) && __ENV.ALLOW_PRODUCTION !== 'true') {
    throw new Error('Production load tests require ALLOW_PRODUCTION=true. Prefer staging.');
}

export const options = {
    stages: __ENV.VUS
        ? [
            { duration: __ENV.RAMP_UP || '30s', target: Number(__ENV.VUS) },
            { duration: __ENV.DURATION || '2m', target: Number(__ENV.VUS) },
            { duration: '15s', target: 0 },
        ]
        : profiles[profile],
    thresholds: {
        http_req_failed: ['rate<0.01'],
        http_req_duration: ['p(95)<800', 'p(99)<1500'],
        checks: ['rate>0.99'],
    },
};

const pages = ['/', '/produtos'];

export default function () {
    for (const path of pages) {
        const response = http.get(`${baseUrl}${path}`, {
            ...requestOptions,
            tags: { page: path },
        });

        check(response, {
            [`${path} returns 200`]: (result) => result.status === 200,
            [`${path} has HTML`]: (result) => result.headers['Content-Type']?.includes('text/html'),
            [`${path} has request ID`]: (result) => Boolean(result.headers['X-Request-Id']),
        });

        sleep(1);
    }
}

export function setup() {
    const response = http.get(`${baseUrl}/health`, requestOptions);

    check(response, {
        'readiness returns 200': (result) => result.status === 200,
        'dependencies are ready': (result) => result.json('status') === 'ok',
    });
}

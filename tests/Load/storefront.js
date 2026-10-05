import http from 'k6/http';
import { check, sleep } from 'k6';
import encoding from 'k6/encoding';

const baseUrl = (__ENV.BASE_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
const basicAuth = __ENV.BASIC_AUTH_USER && __ENV.BASIC_AUTH_PASSWORD
    ? `Basic ${encoding.b64encode(`${__ENV.BASIC_AUTH_USER}:${__ENV.BASIC_AUTH_PASSWORD}`)}`
    : null;
const requestOptions = basicAuth ? { headers: { Authorization: basicAuth } } : {};

if (baseUrl.includes('loja.malu-store.com') && __ENV.ALLOW_PRODUCTION !== 'true') {
    throw new Error('Production load tests require ALLOW_PRODUCTION=true. Prefer staging.');
}

export const options = {
    stages: [
        { duration: __ENV.RAMP_UP || '30s', target: Number(__ENV.VUS || 20) },
        { duration: __ENV.DURATION || '2m', target: Number(__ENV.VUS || 20) },
        { duration: '15s', target: 0 },
    ],
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

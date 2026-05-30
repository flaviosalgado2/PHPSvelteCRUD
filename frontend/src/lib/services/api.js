import { PUBLIC_API_URL } from '$env/static/public';

class ApiError extends Error {
  constructor(message, status, errors = null) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.errors = errors;
  }
}

async function request(path, options = {}) {
  const url = `${PUBLIC_API_URL}${path}`;
  const config = {
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    ...options,
  };

  const response = await fetch(url, config);

  if (!response.ok) {
    let payload;
    try {
      payload = await response.json();
    } catch {
      payload = {};
    }
    throw new ApiError(
      payload.message ?? `Erro HTTP ${response.status}`,
      response.status,
      payload.errors ?? null
    );
  }

  if (response.status === 204) return null;
  return response.json();
}

export const api = {
  get: (path) => request(path),

  post: (path, body) =>
    request(path, { method: 'POST', body: JSON.stringify(body) }),

  put: (path, body) =>
    request(path, { method: 'PUT', body: JSON.stringify(body) }),

  del: (path) => request(path, { method: 'DELETE' }),
};

export { ApiError };

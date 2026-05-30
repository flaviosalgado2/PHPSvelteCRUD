import { api } from './api.js';

export const pessoasService = {
  listar: () => api.get('/pessoas'),

  buscar: (id) => api.get(`/pessoas/${id}`),

  criar: (dados) => api.post('/pessoas', dados),

  atualizar: (id, dados) => api.put(`/pessoas/${id}`, dados),

  excluir: (id) => api.del(`/pessoas/${id}`),

  estatisticas: () => api.get('/pessoas/statistics'),
};

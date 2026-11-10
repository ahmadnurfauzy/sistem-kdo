import client from './client'

/**
 * Modul API kelola akun pengguna (khusus role admin).
 */
export default {
  list: (params) => client.get('/akun', { params }),
  get: (id) => client.get(`/akun/${id}`),
  create: (payload) => client.post('/akun', payload),
  update: (id, payload) => client.put(`/akun/${id}`, payload),
  remove: (id) => client.delete(`/akun/${id}`),
}

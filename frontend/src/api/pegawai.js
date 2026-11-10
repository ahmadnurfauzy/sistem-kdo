import client from './client'

export default {
  list: (params) => client.get('/pegawai', { params }),
  get: (id) => client.get(`/pegawai/${id}`),
  create: (payload) => client.post('/pegawai', payload),
  update: (id, payload) => client.put(`/pegawai/${id}`, payload),
  remove: (id) => client.delete(`/pegawai/${id}`),
}

import client from './client'

export default {
  list: (params) => client.get('/perangkat-daerah', { params }),
  get: (id) => client.get(`/perangkat-daerah/${id}`),
  create: (payload) => client.post('/perangkat-daerah', payload),
  update: (id, payload) => client.put(`/perangkat-daerah/${id}`, payload),
  remove: (id) => client.delete(`/perangkat-daerah/${id}`),
}

import client from './client'

export default {
  list: (params) => client.get('/kendaraan', { params }),
  get: (id) => client.get(`/kendaraan/${id}`),
  create: (payload) => client.post('/kendaraan', payload),
  update: (id, payload) => client.put(`/kendaraan/${id}`, payload),
  remove: (id) => client.delete(`/kendaraan/${id}`),
  assignPJ: (id, pegawaiId) => client.put(`/kendaraan/${id}/penanggung-jawab`, { pegawai_id: pegawaiId }),
}

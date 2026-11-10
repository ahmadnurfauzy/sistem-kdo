import client from './client'

export default {
  kondisi: () => client.get('/laporan/kondisi'),
}

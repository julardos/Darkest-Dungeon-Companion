import axios from 'axios'

const http = axios.create({ baseURL: '/api' })

export const fetchHeroes    = ()                    => http.get('/heroes').then(r => r.data)
export const fetchProvisions = (location, length)   => http.get('/provisions', { params: { location, length } }).then(r => r.data)
export const fetchCurios    = ()                    => http.get('/curios').then(r => r.data)

import axios from 'axios';

const URL_MATCHES = '/api/match',
    URL_MATCH = '/api/match/',
    URL_MOVE = '/api/match/',
    URL_CREATE = '/api/match',
    URL_DELETE = '/api/match/';

export default {
    matches: () => {
        return axios.get(URL_MATCHES)
    },
    match: ({id, player}) => {
        return axios.post(URL_MATCH + id, {
            player: player
        })
    },
    move: ({id, position, player}) => {
        return axios.put(URL_MOVE + id, {
            position: position,
            player: player,
        })
    },
    create: () => {
        return axios.post(URL_CREATE)
    },
    destroy: ({id}) => {
        return axios.delete(URL_DELETE + id)
    },
}
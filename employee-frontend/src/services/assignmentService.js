import api from '@/api/axios'

const authHeaders = () => ({
    headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
    }
})

export default {

    async getAssignments(projectId) {

        const response = await api.get(
            `/projects/${projectId}/assignments`,
            authHeaders()
        )

        return response.data
    },

    async createAssignment(projectId, data) {

        const response = await api.post(
            `/projects/${projectId}/assignments`,
            data,
            authHeaders()
        )

        return response.data
    },

    async updateAssignment(id, data) {

        const response = await api.put(
            `/assignments/${id}`,
            data,
            authHeaders()
        )

        return response.data
    },

    async deleteAssignment(id) {

        const response = await api.delete(
            `/assignments/${id}`,
            authHeaders()
        )

        return response.data
    },

    async getMyAssignments() {
        const response = await api.get(
            `/my-assignments`,
            authHeaders()
        )
        return response 
    }

}
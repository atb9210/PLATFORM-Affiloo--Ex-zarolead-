import axios from 'axios'
import Vue from 'vue'

const state = {
  jobs: [],
}

const getters = {
  allJobs: (state) => state.jobs,
}

const actions = {
  async initializeJobs({commit, dispatch}) {
    await dispatch('fetchJobs')
    setInterval(function() {
      dispatch('updateJobsStatus')
    }.bind(this), 2000);
  },
  async fetchJobs({commit}) {
    // Get jobs stored in cookies
    let storedJobs = []
    let tmp = localStorage.getItem('jobs')
    if (tmp != null) {
      storedJobs = JSON.parse(tmp)
    }
    commit('setJobs', storedJobs)
  },
  async updateJobStatus({commit}, jobId) {
    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve domains
    const response = await axios.get('/api/v1/zarocloner/jobs/' + jobId, { headers })

    commit('setJobStatus', response.data.data)
  },
  async updateJobsStatus({commit, dispatch}) {
    for (let i = 0; i < state.jobs.length; i++) {
      if (state.jobs[i].status == 'executing' || state.jobs[i].status == 'queued') {
        await dispatch('updateJobStatus', state.jobs[i].id)
        commit('saveJobs')
      }
    }
  },
}

const mutations = {
  addJob(state, job) {
    state.jobs.push(job)
    localStorage.setItem('jobs', JSON.stringify(state.jobs))
    VueInstance.$vs.notify({
      title: 'New job: ' + job.name,
      text: 'New job running in background...',
      color: 'primary',
      icon: 'settings',
      position: 'top-right',
    })
  },
  resetJobs(state) {
    state.jobs = []
    localStorage.setItem('jobs', JSON.stringify(state.jobs))
  },
  setJobStatus(state, job) {
    for (let i = 0; i < state.jobs.length; i++) {
      if (state.jobs[i].id == job.id) {
        if (state.jobs[i].status != job.status) {
          let oldStatus = state.jobs[i].status
          let newStatus = job.status

          let message = ''
          let color = ''
          if (oldStatus == 'running') {
            if (newStatus == 'completed') {
              message = 'Job successfully completed.'
              color = 'success'
            } else if (newStatus == 'failed') {
              message = 'Job terminated with an error.'
              color = 'danger'
            }
            VueInstance.$vs.notify({
              title: 'Job: ' + job.name,
              text: message,
              color: color,
              icon: 'settings',
              position: 'top-right',
            })
          }

          state.jobs[i].status = job.status
        }
        break
      }
    }
    localStorage.setItem('jobs', JSON.stringify(state.jobs))
  },
  setJobs(state, jobs) {
    state.jobs = jobs
    localStorage.setItem('jobs', JSON.stringify(state.jobs))
  },
  saveJobs(state) {
    localStorage.setItem('jobs', JSON.stringify(state.jobs))
  },
}

export default {
    state,
    getters,
    actions,
    mutations,
}

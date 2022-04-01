import _ from 'lodash'

export function copyObject(src, dst) {
  for (const [key, value] of Object.entries(src)) {
    dst[key] = value
  }
  return dst
}

export function cloneObject(obj) {
  let newObj = {...obj}
  return newObj
}

export function deepCloneObject(obj) {
  return _.cloneDeep(obj)
}

export function formatDate(dateObj) {
  let dd = String(dateObj.getDate()).padStart(2, '0')
  let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
  let yyyy = dateObj.getFullYear()
  
  let formattedDate = yyyy + '-' + mm + '-' + dd

  return formattedDate
}

export function formatDateTime(dateObj) {
  let dd = String(dateObj.getDate()).padStart(2, '0')
  let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
  let yyyy = dateObj.getFullYear()
  let hh = String(dateObj.getHours()).padStart(2, '0')
  let ii = String(dateObj.getMinutes()).padStart(2, '0')
  
  let formattedDateTime = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii

  return formattedDateTime
}

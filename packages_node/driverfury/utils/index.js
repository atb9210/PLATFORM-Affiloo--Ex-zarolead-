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

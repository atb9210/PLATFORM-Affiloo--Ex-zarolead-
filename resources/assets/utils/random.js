export default {
  randomString(length) {
    var result           = []
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    var charactersLength = characters.length
    for ( var i = 0; i < length; i++ ) {
      result.push(characters.charAt(Math.floor(Math.random()*charactersLength)))
    }
    return result.join('')
  },
  randomDigit() {
    var result           = []
    var characters       = '0123456789'
    var charactersLength = characters.length
    for ( var i = 0; i < 1; i++ ) {
      result.push(characters.charAt(Math.floor(Math.random()*charactersLength)))
    }
    return result.join('')
  },
}

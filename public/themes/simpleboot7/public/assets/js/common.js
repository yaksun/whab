function  setCookie(name,val,iDay){
    var oDate = new Date()

    oDate.setDate(oDate.getDate()+iDay)
    
    document.cookie = name+'='+val+';expires='+oDate+';path=/ '
}


function getCookie(name){

var arr = document.cookie.split('; ')

for(var i = 0;i< arr.length;i++){
    var temp = arr[i].split('=')
    
//  console.log(temp)
  
  if(temp[0] == name){
        return temp[1]
  }

}

}
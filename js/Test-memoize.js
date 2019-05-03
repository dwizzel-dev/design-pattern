
//JAVASCRIPT
//https://codeburst.io/understanding-memoization-in-3-minutes-2e58daf33a19

// A more functional memoizer

//We can beef up our module by adding functions later
var Memoizer = (function(){
    //Private data
   var cache = {};
   //named functions are awesome!
   function cacher(func){
      return function(){
        var key = JSON.stringify(arguments);
        if(cache[key]){
          return cache[key];
        }
        else{
          val = func.apply(this, arguments);
          cache[key] = val;
          return val;
      }
    }
  }    
    //Public data
   return{
     memo: function(func){
       return cacher(func);
     }
   }
})()


var fib = Memoizer.memo(function(n){
  if (n < 2){
     return 1;
   }else{
     return fib(n-2) + fib(n-1);
   }
});



var PermutateArr = function(arrWord){
	var results = [];
	function permute(arr, memo){
		var cur, memo = memo || [];
		for(var i = 0; i < arr.length; i++){
			cur = arr.splice(i, 1);
			if(arr.length === 0){
				results.push(memo.concat(cur));
			}
			permute(arr.slice(), memo.concat(cur));
			arr.splice(i, 0, cur[0]);
		}
		return results;
	}
	return permute(arrWord);
};

var RegexWordPermutation = function(arr){
	var arrRes = PermutateArr(arr);
	var tmp = [''];
	arrRes.forEach(function(item){
		var str1 = '';
		for(var o in item){
			str1 += '\\s' + item[o] + '.*';
		}
		this[0] += str1 + '|';
	}, tmp);
	tmp = tmp[0].substring(0, (tmp[0].length - 1));
	return '.*(?:' + tmp  + ')';
};

var CreateAndRegex = function(arr){
  var tmp = '';
  arr.forEach(function(item){
    tmp += '(?=.*\\s' + item + ')';
  });
  return '(?:' + tmp + ')';
};


//var s2 = RegexWordPermutation("doloremque middling ending unde sit".split(' '));
var s3 = CreateAndRegex("start doloremque    error       middling ending unde sit".replace(/\s+/g, ' ').split(' '));

var test = [];
var max = 1000;
for(var i=0; i<max; i++){
  test.push(" starting Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium middling doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt ending ");
}

//console.log(s2);
console.log(s3);

/*
var timer = new Date().getTime();
var t2 = test.filter(function(o){
	return RegExp(s2, 'gi').test(o);
});
console.log((new Date().getTime() - timer) + 'ms');
*/

var timer = new Date().getTime();
var t2 = test.filter(function(o){
	return RegExp(s3, 'gi').test(o);
});
console.log((new Date().getTime() - timer) + 'ms');
console.log(t2.length);



/*
console.log(s2);
console.log('');
console.log(s3);
console.log('');
*/



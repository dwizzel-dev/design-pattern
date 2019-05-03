/*


HTML:
<button class="butt">hello</button>


CSS:
.butt{color:#fff;padding:30px;border-radius:10px;border:0;font-size:1.5rem}
.red{background-color:#f00;}
.blue{background-color:#00f;}


JAVASCRIPT:

*/


//-------------------

// +++ ES5 GOOD SCOPE for this +++

function onClick(ev){
  let style = ev.target.classList;
  style.remove('red');
  style.add('blue');
}

let test = {
	name: 'Test',	
	butt: document.querySelector('.butt'),
	init: function(){
      this.butt.addEventListener('click', onClick);
      this.butt.addEventListener('mouseover', function(ev){
        let style = ev.target.classList;
        style.remove('blue');
        style.add('red');
        console.log(this.name);
      }.bind(this));
    }
}

test.init();


//-------------------

// +++ ES6 GOOD SCOPE for this +++

function onClick(ev){
  let style = ev.target.classList;
  style.remove('red');
  style.add('blue');
}

let test = {
	counter: 0,
	butt: document.querySelector('.butt'),
	init: function(){
      let out = 'MouseOver';
      this.butt.addEventListener('click', onClick);
      this.butt.addEventListener('mouseover', (ev) => {
		let target = ev.target;
        let style = target.classList;
        style.remove('blue');
        style.add('red');
        target.textContent = `${out} ${this.counter++}`;
      });
    }
}

test.init();




















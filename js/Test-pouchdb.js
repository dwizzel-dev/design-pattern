
// //cdn.jsdelivr.net/npm/pouchdb@7.0.0/dist/pouchdb.min.js


/*
HTML:

<input type="text" id="todo" value="" placeholder="enter todos">
<button id="add">add</button>
<button id="show">show</button>
<button id="clear">clear all</button>
<div>
  <h3>Todo list</h3>
  <ul id="todoList"></ul>
</div>


CSS:

button{
  padding:10px;
  border-radius:7px;
  border:1px solid #aaa;
}
input[type=text]{
  padding:9px;
  border-radius:7px;
  border:1px solid #aaa;
}
UL{
  padding:0 5px;
}
LI{
  cursor: pointer;
  padding: 0 0 10px;
  list-style: none;
}


*/

// //cdn.jsdelivr.net/npm/pouchdb@7.0.0/dist/pouchdb.min.js


// //cdn.jsdelivr.net/npm/pouchdb@7.0.0/dist/pouchdb.min.js

var db = new PouchDB('todos');

_ = {
  gid: id => document.getElementById(id),
  cel: el => document.createElement(el),
  ctn: el => document.createTextNode(el),
  ac: (e, n) => e.appendChild(n),
  sa: (e, n, v) => e.setAttribute(n, v),
  ael: (e, n, c) => e.addEventListener(n, c),
  ga: (e, n) => e.getAttribute(n),
}

_.ael(_.gid('todoList'), 'click', function(ev){
  var el = ev.target;
  removeTodo(_.ga(el, 'id'), _.ga(el, 'rev'));
});
_.ael(_.gid('add'), 'click', recordTodo);
_.ael(_.gid('show'), 'click', showTodos);
_.ael(_.gid('clear'), 'click', clearTodos);



function removeTodo(id, rev){
  console.log(id, rev);
  db.remove(id, rev, function(err, result){
    console.log(result);  
    showTodos();
  });
}

function addTodo(text) {
  var todo = {
    title: text,
    completed: false
  };
  var el = _.gid('todoList');
  db.post(todo, function(err, result) {
    if (!err) {
      console.log(result);
      var node = _.cel("LI");
      _.sa(node, 'id', result.id);
      _.sa(node, 'rev', result.rev);
      _.ac(node, _.ctn(todo.title)); 
      _.ac(el, node);  
    }
  });
}

function showTodos() {
  var el = _.gid('todoList');
  el.innerHTML = '';
  db.allDocs({include_docs: true, descending: true}, function(err, doc) {
    doc.rows.forEach(function(row, index){
      console.log(row);
      var node = _.cel("LI");
      _.sa(node, 'id', row.id);
      _.sa(node, 'rev', row.value.rev);
      _.ac(node, _.ctn(row.doc.title)); 
      _.ac(el, node);
    });
  });
}


function recordTodo() {
  let el = _.gid('todo');
  if(el.value !== ''){
    addTodo(el.value);
    el.value = "";  
  }
  _.gid('todo').focus();
}


function clearTodos(){
  db.destroy().then(function (response) {
    var el = _.gid('todoList');
    el.innerHTML = '';
    db = new PouchDB('todos');
    console.log(response);
  }).catch(function (err) {
    console.log(err);
  });
  
}






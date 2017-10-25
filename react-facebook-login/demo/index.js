import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { Router, Route, Link, browserHistory } from 'react-router';
import FacebookLogin from '../src/facebook';
import axios from 'axios'

const responseFacebook = (response) => {
  console.log(response)
  axios.post('http://localhost:8000/api/auth', response)
    .then(res => {
      alert(`
          Name : ${response.name} 
          Token : ${res.data.token}
          `)
    })
    .catch(err => {
      console.log(err)
    })
};

class Base extends Component {
  render() {
    return (
      <div>
        <Link to="/dummy">Route to dummy page</Link>
        <FacebookLogin
          appId="117596685239997"
          autoLoad
          callback={responseFacebook}
          icon="fa-facebook"
        />
      </div>
    );
  }
}

class Dummy extends Component {
  render() {
    return (
      <div>
        <Link to="/">Back</Link>
        <h1>
          This is just a dummy page to test the button<br />
          <a href="https://github.com/keppelen/react-facebook-login/pull/76#issuecomment-262098946">
            survives back and forth routing
          </a>
        </h1>
      </div>
    );
  }
}

ReactDOM.render(
  <Router history={browserHistory}>
    <Route path="/" component={Base}/>
    <Route path="/dummy" component={Dummy}/>
  </Router>,
  document.getElementById('demo')
);

import ReactDOM from 'react-dom';
import React from 'react';

import '../scss/app.scss';
import Application from '../tsx/Application.tsx';
import ReactApplication from '../tsx/ReactApplication';

let element;

element = document.getElementById('app-root');
if (element) {
  ReactDOM.render(<Application />, element);
}

element = document.getElementById('react-root');
if (element) {
  ReactDOM.render(<ReactApplication />, element);
}

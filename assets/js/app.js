import ReactDOM from 'react-dom';
import React from 'react';

import '../scss/app.scss';
import Application from '../tsx/Application.tsx';

const element = document.getElementById('app-root');
if (element) {
  ReactDOM.render(<Application />, element);
}

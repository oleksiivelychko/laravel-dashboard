const wait = (delay = 0) =>
    new Promise(resolve => setTimeout(resolve, delay));

const setVisible = (elementOrSelector, visible) =>
    (typeof elementOrSelector === 'string'
            ? document.querySelector(elementOrSelector)
            : elementOrSelector
    ).style.display = visible ? 'block' : 'none';

//setVisible('.loader', false);
setVisible('#loader', true);

document.addEventListener('DOMContentLoaded', () =>
    wait(800).then(() => {
        //setVisible('.loader', true);
        setVisible('#loader', false);
    }));

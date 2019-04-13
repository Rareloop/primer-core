export default (html) => {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.children[0];
};

import Search from './search';
import SidebarSmall from './sidebar.small';

const sidebar = document.querySelector('.primer-sidebar');
sidebar.classList.add('primer-sidebar--enhanced');

// Handle top level sections
const sections = document.querySelectorAll('.primer-sidebar-group');

[].forEach.call(sections, (section) => {
    section.querySelector('.primer-sidebar-group__title').addEventListener('click', (event) => {
        event.preventDefault();

        section.classList.toggle('primer-sidebar-group--open');
    });
});

// Handle Tree
const groupNodeTitles = document.querySelectorAll('.primer-nav-item--has-children > .primer-nav-item__link');

[].forEach.call(groupNodeTitles, (node) => {
    node.addEventListener('click', (event) => {
        if (event.target.nodeName === 'A') {
            return;
        }

        event.preventDefault();

        const groupNode = node.closest('.primer-nav-item');

        groupNode.classList.toggle('primer-nav-item--open');

        if (groupNode.classList.contains('primer-nav-item--open')) {
            groupNode.classList.remove('primer-nav-item--closed');
        } else {
            groupNode.classList.add('primer-nav-item--closed');
        }
    });
});

new Search(document.querySelector('.primer-search-container'), document.querySelector('.primer-sidebar__nav'));
new SidebarSmall(document.querySelector('.primer-sidebar'));

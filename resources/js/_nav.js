export default [
  {
    component: 'CNavItem',
    name: 'Dashboard',
    to: '/dashboard',
    classIcon: 'cil-speedometer',
  },
  {
    component: 'CNavItem',
    name: 'Companies',
    to: '/companies',
    classIcon: 'cil-spreadsheet',
  },
  {
    component: 'CNavGroup',
    name: 'Places',
    to: '/places',
    classIcon: 'cil-library',
    items: [
      {
        component: 'CNavItem',
        name: 'Locations',
        to: '/places/locations',
      },
      {
        component: 'CNavItem',
        name: 'Tables',
        to: '/places/tables',
      },
    ],
  },
  {
    component: 'CNavItem',
    name: 'Events',
    to: '/events',
    classIcon: 'cil-calendar',
  },
  {
    component: 'CNavGroup',
    name: 'People',
    to: '/people',
    classIcon: 'cil-people',
    items: [
      {
        component: 'CNavItem',
        name: 'Attendee Groups',
        to: '/people/attendee-groups',
      },
      {
        component: 'CNavItem',
        name: 'Attendees',
        to: '/people/attendees',
      },
    ],
  },
  {
    component: 'CNavItem',
    name: 'Booking',
    to: '/booking',
    classIcon: 'cil-notes',
  },
]

export const stateClasses = [
    {
        value : 'default',
        label : 'Predeterminado',
        classes: ''
    },{
        value : 'green',
        label : 'Verde',
        classes: `
            bg-green-100 text-green-800 border-green-500
            dark:bg-green-900 dark:text-green-200
        `,
        active: `
            bg-green-500 text-white
        `
    },{
        value : 'yellow',
        label : 'Amarillo',
        classes : `
            bg-yellow-100 text-yellow-800 border-yellow-500
            dark:bg-yellow-700 dark:text-yellow-200
        `,
        active: `
            bg-yellow-500 text-white
        `
    },{
        value : 'red',
        label : 'Rojo',
        classes : `
            bg-red-100 text-red-800 border-red-500
            dark:bg-red-900 dark:text-red-200
        `,
        active: `
            bg-red-500 text-white
        `
    },{
        value : 'violet',
        label : 'Violeta',
        classes :  `
            bg-violet-100 text-violet-800 border-violet-500
            dark:bg-violet-900 dark:text-violet-200
        `,
        active: `
            bg-violet-500 text-white
        `
    },{
        value : 'blue',
        label : 'Azul',
        classes : `
            bg-blue-100 text-blue-800 border-blue-500
            dark:bg-blue-900 dark:text-blue-200
        `,
        active: `
            bg-blue-500 text-white
        `
    },{
        value : 'orange',
        label : 'Naranja',
        classes : `
            bg-orange-100 text-orange-800 border-orange-500
            dark:bg-orange-900 dark:text-orange-200
        `,
        active: `
            bg-orange-500 text-white
        `
    },{
        value : 'teal',
        label : 'Verde Agua',
        classes : `
            bg-teal-100 text-teal-800 border-teal-500
            dark:bg-teal-900 dark:text-teal-200
        `,
        active: `
            bg-teal-500 text-white
        `
    },{
        value : 'fuchsia',
        label : 'Fucsia',
        classes : `
            bg-fuchsia-100 text-fuchsia-800 border-fuchsia-500
            dark:bg-fuchsia-900 dark:text-fuchsia-200
        `,
        active: `
            bg-fuchsia-500 text-white
        `
    },{
        value : 'stone',
        label : 'Gris',
        classes : `
            bg-stone-200 text-stone-700 border-stone-500
            dark:bg-stone-800 dark:text-stone-300
        `,
        active: `
            bg-stone-500 text-white
        `
    },{
        value : 'slate',
        label : 'Pizarra',
        classes : `
            bg-slate-200 text-slate-700 border-slate-500
            dark:bg-slate-800 dark:text-slate-200
        `,
        active: `
            bg-slate-500 text-white
        `
    },{
        value : 'mist',
        label : 'Neblina',
        classes : `
            bg-mist-200 text-mist-800 border-mist-500
            dark:bg-mist-900 dark:text-mist-200
        `,
        active: `
            bg-mist-500 text-white
        `
    }
]

export function getStateClasses(value) {
    return stateClasses.find(
        state => state.value === value
    )?.classes ?? ''
}

export function getStateActive(value) {
    return stateClasses.find(
        state => state.value === value
    )?.active ?? ''
}

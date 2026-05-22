export const stateClasses = [
    {
        value : 'default',
        label : 'Predeterminado',
        classes: ''
    },{
        value : 'green',
        label : 'Verde',
        classes: `
            bg-green-100 text-green-800
            dark:bg-green-900 dark:text-green-200
        `
    },{
        value : 'yellow',
        label : 'Amarillo',
        classes : `
            bg-yellow-100 text-yellow-800
            dark:bg-yellow-700 dark:text-yellow-200
        `
    },{
        value : 'red',
        label : 'Rojo',
        classes : `
            bg-red-100 text-red-800
            dark:bg-red-900 dark:text-red-200
        `
    },{
        value : 'violet',
        label : 'Violeta',
        classes :  `
            bg-violet-100 text-violet-800
            dark:bg-violet-900 dark:text-violet-200
        `
    },{
        value : 'blue',
        label : 'Azul',
        classes : `
            bg-blue-100 text-blue-800
            dark:bg-blue-900 dark:text-blue-200
        `
    },{
        value : 'orange',
        label : 'Naranja',
        classes : `
            bg-orange-100 text-orange-800
            dark:bg-orange-900 dark:text-orange-200
        `
    },{
        value : 'Verde agua',
        label : 'teal',
        classes : `
            bg-teal-100 text-teal-800
            dark:bg-teal-900 dark:text-teal-200
        `,
    },{
        value : 'fuchsia',
        label : 'Fucsia',
        classes : `
            bg-fuchsia-100 text-fuchsia-800
            dark:bg-fuchsia-900 dark:text-fuchsia-200
        `
    },{
        value : 'stone',
        label : 'Gris',
        classes : `
            bg-stone-200 text-stone-700
            dark:bg-stone-800 dark:text-stone-300
        `
    },{
        value : 'slate',
        label : 'Pizarra',
        classes : `
            bg-slate-200 text-slate-700
            dark:bg-slate-800 dark:text-slate-200
        `
    },{
        value : 'mist',
        label : 'Neblina',
        classes : `
            bg-mist-200 text-mist-800
            dark:bg-mist-900 dark:text-mist-200
        `
    }
]

export function getStateClasses(value) {
    return stateClasses.find(
        state => state.value === value
    )?.classes ?? ''
}


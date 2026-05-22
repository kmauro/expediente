import { useColorMode } from '@vueuse/core'

export function useTheme() {

    const mode = useColorMode()

    return {
        mode,
    }
}

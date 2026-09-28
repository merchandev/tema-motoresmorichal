import Swiper from 'swiper'
import { FreeMode, Keyboard, Navigation, Thumbs } from 'swiper/modules'

// Only the modules the runtime configures (hero, catalog and gallery sliders)
// are bundled; the full swiper/bundle tripled the download for no benefit.
Swiper.use([Navigation, Thumbs, FreeMode, Keyboard])

// The proven front runtime consumes Swiper as a browser global. Keeping this
// bridge in its own entry guarantees that WordPress can enforce load order.
Object.assign(window, { Swiper })

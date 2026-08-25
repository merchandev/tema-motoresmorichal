import Swiper from 'swiper/bundle'

// The proven front runtime consumes Swiper as a browser global. Keeping this
// bridge in its own entry guarantees that WordPress can enforce load order.
Object.assign(window, { Swiper })

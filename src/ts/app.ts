import Swiper from 'swiper'
import 'swiper/css'

// Type declaration for global Swiper
declare const Swiper: any

// Slider principal de videos
const hero = document.querySelector('#custom-slider')
if (hero) {
  // eslint-disable-next-line no-new
  new Swiper('#custom-slider', {
    loop: false,
    navigation: { nextEl: '.cs-swiper-button-next', prevEl: '.cs-swiper-button-prev' },
    observer: true,
    observeParents: true,
  })
}

// Carrusel de vehículos
const vehiculos = document.querySelector('#vehiculos .toyota-slider')
if (vehiculos) {
  // eslint-disable-next-line no-new
  new Swiper('#vehiculos .toyota-slider', {
    spaceBetween: 20,
    navigation: {
      nextEl: '#vehiculos .toyota-arrow.swiper-button-next',
      prevEl: '#vehiculos .toyota-arrow.swiper-button-prev'
    },
    breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
  })
}

// ==========================================
// VEHICLE COLOR SELECTOR - ISOLATED MODULE
// ==========================================

interface ColorButton extends HTMLButtonElement {
  dataset: {
    img: string
    name: string
  }
}

class VehicleColorSelector {
  private colorButtons: NodeListOf<ColorButton>
  private colorName: HTMLElement | null
  private mainImage: HTMLImageElement | null
  private ctaButton: HTMLAnchorElement | null

  constructor() {
    this.colorButtons = document.querySelectorAll<ColorButton>('.vp-color-btn')
    this.colorName = document.getElementById('vp-color-name')
    this.mainImage = document.querySelector<HTMLImageElement>('.vp-car-img')
    this.ctaButton = document.querySelector<HTMLAnchorElement>('.vp-btn-primary[data-wa]')

    this.init()
  }

  private init(): void {
    if (this.colorButtons.length === 0) return

    this.colorButtons.forEach(btn => {
      btn.addEventListener('click', () => this.handleColorClick(btn))
    })

    if (this.ctaButton) {
      this.ctaButton.addEventListener('click', (e) => this.handleCTAClick(e))
    }
  }

  private handleColorClick(clickedBtn: ColorButton): void {
    // Remove active state from all buttons
    this.colorButtons.forEach(btn => btn.classList.remove('is-active'))

    // Add active state to clicked button
    clickedBtn.classList.add('is-active')

    // Update color name
    if (this.colorName && clickedBtn.dataset.name) {
      this.colorName.textContent = clickedBtn.dataset.name
    }

    // Update image with fade transition
    if (this.mainImage && clickedBtn.dataset.img) {
      this.mainImage.style.opacity = '0'
      setTimeout(() => {
        if (this.mainImage) {
          this.mainImage.src = clickedBtn.dataset.img
          this.mainImage.style.opacity = '1'
        }
      }, 200)
    }
  }

  private handleCTAClick(e: Event): void {
    e.preventDefault()

    const activeSwatch = document.querySelector<ColorButton>('.vp-color-btn.is-active')
    const colorName = activeSwatch?.dataset.name || ''
    const modelo = this.ctaButton?.dataset.modelo || ''
    const version = this.ctaButton?.dataset.version || ''

    const txt = `Hola, quisiera cotizar el ${modelo} ${version ? '(' + version + ')' : ''} en color ${colorName}.`

    if (this.ctaButton?.dataset.wa) {
      window.open(this.ctaButton.dataset.wa + encodeURIComponent(txt), '_blank')
    }
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new VehicleColorSelector())
} else {
  new VehicleColorSelector()
}

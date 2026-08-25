// ==========================================
// VEHICLE COLOR SELECTOR - ISOLATED MODULE
// ==========================================

type ColorButton = HTMLButtonElement & {
  dataset: DOMStringMap & {
    img?: string
    name?: string
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
    const browserWindow = window as Window & { vpColorInit?: boolean }
    if (browserWindow.vpColorInit) return
    browserWindow.vpColorInit = true

    this.colorButtons.forEach(btn => {
      btn.addEventListener('click', () => this.handleColorClick(btn))
    })

    if (this.ctaButton) {
      this.ctaButton.addEventListener('click', (e) => this.handleCTAClick(e))
    }
  }

  private handleColorClick(clickedBtn: ColorButton): void {
    // Remove active state from all buttons
    this.colorButtons.forEach(btn => {
      btn.classList.remove('active', 'is-active')
      btn.setAttribute('aria-pressed', 'false')
    })

    // Add active state to clicked button
    clickedBtn.classList.add('active', 'is-active')
    clickedBtn.setAttribute('aria-pressed', 'true')

    // Update color name
    if (this.colorName && clickedBtn.dataset.name) {
      this.colorName.textContent = clickedBtn.dataset.name
    }

    // Update image with fade transition
    const imageUrl = clickedBtn.dataset.img
    if (this.mainImage && imageUrl) {
      this.mainImage.style.opacity = '0'
      setTimeout(() => {
        if (this.mainImage) {
          this.mainImage.src = imageUrl
          this.mainImage.style.opacity = '1'
        }
      }, 200)
    }
  }

  private handleCTAClick(_event: Event): void {
    const activeSwatch = document.querySelector<ColorButton>('.vp-color-btn.is-active')
    const colorName = activeSwatch?.dataset.name || ''
    const modelo = this.ctaButton?.dataset.modelo || ''
    const version = this.ctaButton?.dataset.version || ''
    const isUsed = this.ctaButton?.dataset.usado === 'true'
    const intent = isUsed ? 'consultar disponibilidad del vehículo usado' : 'cotizar el vehículo'

    const color = colorName ? ` en color ${colorName}` : ''
    const txt = `Hola, quisiera ${intent}: ${modelo}${version ? ' (' + version + ')' : ''}${color}.`

    if (this.ctaButton?.dataset.wa) {
      this.ctaButton.href = this.ctaButton.dataset.wa + encodeURIComponent(txt)
    }
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new VehicleColorSelector())
} else {
  new VehicleColorSelector()
}

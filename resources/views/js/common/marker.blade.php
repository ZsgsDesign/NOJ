<style>
    [data-marker-enabled] {
        outline: 0
    }

    .marker {
        position: fixed;
        z-index: 100;
        display: none;
        box-shadow: 0 2px 7px rgba(0,0,0,.3);
        font-size: .8125rem;
        transition: opacity .1s ease-out,-webkit-transform .1s ease-out;
        transition: transform .1s ease-out,opacity .1s ease-out;
        transition: transform .1s ease-out,opacity .1s ease-out,-webkit-transform .1s ease-out;
        -webkit-transform: scale(.8);
        transform: scale(.8);
        -webkit-transform-origin: 0 100%;
        transform-origin: 0 100%;
        pointer-events: none;
        background: #fff;
        padding: 1px;
        border: 1px solid #aaa;
    }

    .marker.open {
        -webkit-transform: none;
        transform: none;
        pointer-events: auto;
    }

    .marker__toolbar {
        padding: 1px;
        background: #f2f6f7;
        white-space: nowrap;
        font-size: 0;
    }

    .marker__action {
        display: inline-block;
        text-align: center;
        font-size: 1rem;
        vertical-align: top;
        cursor: pointer;
        padding: .5rem;
        color: #8395a1;
        position: relative;
        line-height: 1;
    }

    .marker__action:hover {
        background: #e3e7e8;
        color: #5a6b75;
    }

    .marker__icon {
        display: block;
        margin: .125rem;
        width: .75rem;
        height: .75rem;
        border-radius: 50%;
    }

    .marker__icon.icon-yellow {
        background: #ffc100;
    }

    .marker__icon.icon-green {
        background: #54d651;
    }
</style>

<script>
function _throttle (callback, limit) {
    var waiting = false;
    return function () {
        if (!waiting) {
            callback.apply(this, arguments);
            waiting = true;
            setTimeout(function () {
                waiting = false;
            }, limit);
        }
    }
}
const MARKER_ID = `marker_${Math.floor(Math.random() * 0xFFFFFF).toString(16)}`;
const MARKER_OFFSET = 20;
const MARKER_MAX_DISTANCE = 60;

let markerInstance = null;

function distanceToRect(px, py, rect) {
  const cx = (rect.left + rect.right) / 2;
  const cy = (rect.top + rect.bottom) / 2;
  const dx = Math.max(Math.abs(px - cx) - (rect.right - rect.left) / 2, 0);
  const dy = Math.max(Math.abs(py - cy) - (rect.bottom - rect.top) / 2, 0);
  return Math.sqrt(dx * dx + dy * dy);
}

class Marker {
  static exists() {
    return markerInstance && document.getElementById(MARKER_ID);
  }

  constructor() {
    if (Marker.exists()) {
      return markerInstance;
    }
    if (markerInstance) {
      markerInstance.destroy();
    }
    this.$dom = $(`
      <div class="marker" id="${MARKER_ID}"><div class="marker__toolbar">
        <div class="marker__action" data-color="#ffeb3b" data-tooltip="Mark Yellow"><span class="MDI checkbox-blank-circle wemd-yellow-text"></span></div>
        <div class="marker__action" data-color="#8bc34a" data-tooltip="Mark Green"><span class="MDI checkbox-blank-circle wemd-light-green-text"></span></div>
        <div class="marker__action" data-color="transparent" data-tooltip="Clear Marks"><span class="MDI eraser"></span></div>
      </div></div>
    `)
      .appendTo('body');
    this.$dom.find('.marker__toolbar').on('click', '.marker__action', this.onMarkerActionClick.bind(this));
    this.$dom.on('mousedown', this.onMarkerMouseDown.bind(this));
    this.isOpen = false;
    this.bindedHandlers = false;
    this._onKeyDown = this.onKeyDown.bind(this);
    this._onScroll = _throttle(this.onScroll.bind(this), 50);
    this._onMouseDown = this.onMouseDown.bind(this);
    this._onMouseMove = _throttle(this.onMouseMove.bind(this), 50);
    markerInstance = this;
  }

  markSelection(color) {
    const selection = window.getSelection();
    const range = selection.getRangeAt(0);
    this.$container.attr('contentEditable', true);
    if (range) {
      // recover ranges
      selection.removeAllRanges();
      selection.addRange(range);
    }
    if (!document.execCommand('HiliteColor', false, color)) {
      document.execCommand('BackColor', false, color);
    }
    this.$container.removeAttr('contentEditable');
    if (range) {
      // remove ranges after marking
      selection.removeAllRanges();
    }
  }

  showAtPosition($container, x, y) {
    this.$container = $container;
    this.$dom.css({
      display: 'block',
      opacity: 0,
    });
    const rect = this.$dom[0].getBoundingClientRect();
    this.$dom.css({
      left: x + MARKER_OFFSET,
      top: y - (rect.bottom - rect.top) - MARKER_OFFSET,
    });
    this.isOpen = true;
    this.$dom.addClass('open');
    this.updateMarkerOpacity(x, y);
    this.bindEventHandlersForClosing();
  }

  async close() {
    if (!this.isOpen) {
      return;
    }
    this.isOpen = false;
    this.unbindEventHandlers();
    this.$dom
      .css({ opacity: 0 })
      .removeClass('open');
    await delay(200);
    if (!this.isOpen) {
      this.$dom.hide();
    }
  }

  bindEventHandlersForClosing() {
    if (this.bindedHandlers) {
      return;
    }
    $(document).on('keydown', this._onKeyDown);
    $(window).on('scroll', this._onScroll);
    $(document).on('mousedown', this._onMouseDown);
    $(document).on('mousemove', this._onMouseMove);
    this.bindedHandlers = true;
  }

  unbindEventHandlers() {
    if (!this.bindedHandlers) {
      return;
    }
    $(document).off('keydown', this._onKeyDown);
    $(window).off('scroll', this._onScroll);
    $(document).off('mousedown', this._onMouseDown);
    $(document).off('mousemove', this._onMouseMove);
    this.bindedHandlers = false;
  }

  onMarkerActionClick(ev) {
    const color = $(ev.currentTarget).attr('data-color');
    this.markSelection(color);
    this.close();
  }

  onMarkerMouseDown(ev) {
    ev.stopPropagation();
    ev.preventDefault();
  }

  onKeyDown() {
    this.close();
  }

  onScroll() {
    this.close();
  }

  onMouseMove(ev) {
    this.updateMarkerOpacity(ev.clientX, ev.clientY);
  }

  onMouseDown() {
    this.close();
  }

  updateMarkerOpacity(x, y) {
    const markerRect = this.$dom[0].getBoundingClientRect();
    const distance = distanceToRect(x, y, markerRect);
    if (distance > MARKER_MAX_DISTANCE) {
      this.close();
      return;
    }
    this.$dom.css('opacity', 1 - (distance / MARKER_MAX_DISTANCE));
  }

  destroy() {
    this.close();
    this.$dom.remove();
    markerInstance = null;
  }
}

const MarkerInterface = {
  close() {
    if (!markerInstance) {
      return;
    }
    markerInstance.close();
  },
  showAtPosition($container, x, y) {
    const marker = new Marker();
    marker.showAtPosition($container, x, y);
  },
};
</script>

@include('js.common.marker')
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
    border: 1px solid #aaa
}

.marker.open {
    -webkit-transform: none;
    transform: none;
    pointer-events: auto
}

.marker__toolbar {
    padding: 1px;
    background: #f2f6f7;
    white-space: nowrap;
    font-size: 0
}

.marker__action {
    display: inline-block;
    text-align: center;
    font-size: 1rem;
    vertical-align: top;
    cursor: pointer;
    padding: .5rem;
    color: #8395a1;
    position: relative
}

.marker__action:hover {
    background: #e3e7e8;
    color: #5a6b75
}

.marker__icon {
    display: block;
    margin: .125rem;
    width: .75rem;
    height: .75rem;
    border-radius: 50%
}

.marker__icon.icon-yellow {
    background: #ffc100
}

.marker__icon.icon-green {
    background: #54d651
}
</style>
<script>
class MarkerPen {
  static DOMAttachKey = 'nojMarkerPenInstance';
  static uniqueIdCounter = 0;

  static initFromDOM($dom) {
    return MarkerPen.getOrConstruct($dom);
  }

  static initAll() {
    $('[data-marker-enabled]').get().forEach(dom => MarkerPen.initFromDOM($(dom)));
  }

  static get($obj) {
    const key = this.DOMAttachKey;
    return $obj.data(key);
  }

  static getOrConstruct($obj, ...args) {
    const $singleObj = $obj.eq(0);
    const key = this.DOMAttachKey;
    const Protoclass = this;
    const instance = this.get($singleObj);
    if (instance !== undefined) {
      return instance;
    }
    const newInstance = new Protoclass($singleObj, ...args);
    if (!newInstance.$dom) {
      return null;
    }
    newInstance.$dom.data(key, newInstance);
    return newInstance;
  }

  constructor($target) {
    if ($target == null) {
      return null;
    }
    this.$dom = $target;
    this.id = ++this.uniqueIdCounter;
    this.eventNS = `nojobj_${this.id}`;
    this.detached = false;
    this._onMouseUp = this.onMouseUp.bind(this);
    this.bindEventHandlers();
  }

  bindEventHandlers() {
    this.$dom.on('mouseup', this._onMouseUp);
  }

  unbindEventHandlers() {
    this.$dom.off('mouseup', this._onMouseUp);
  }

  async onMouseUp(ev) {
    await delay(1);
    if (!window.getSelection) {
      return;
    }
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0) {
      return;
    }
    const range = selection.getRangeAt(0);
    if (range.collapsed) {
      return;
    }
    MarkerInterface.showAtPosition(this.$dom, ev.clientX, ev.clientY);
  }

  detach() {
    if (this.detached) {
      return;
    }
    this.unbindEventHandlers();
    MarkerInterface.close();
    if (this.constructor.DOMAttachKey) {
      this.$dom.removeData(this.constructor.DOMAttachKey);
    }
    this.detached = true;
  }
}
</script>

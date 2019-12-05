@include('js.common.marker')
<script>
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

class MarkerPen {
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
_defineProperty(MarkerPen, "DOMAttachKey", 'nojMarkerPenInstance');
_defineProperty(MarkerPen, "uniqueIdCounter", 0);
</script>

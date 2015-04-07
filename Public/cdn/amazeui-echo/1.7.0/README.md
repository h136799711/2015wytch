# Echo.js

Forked from [Echo](https://github.com/toddmotto/echo).

Echo 是一个轻量的 JS 图片懒加载插件，不依赖其它库。支持 IE8+。

**使用示例：**

- [与 Amaze UI ScrollSpy 结合使用](http://amazeui.github.io/echo/docs/scrollspy.html)

## 获取 Echo.js

**使用 NPM：**

Amaze UI 只是添加了使用示例并发布到 NPM，代码与官方发布的版本保持一致。

```
npm install amazeui-echo
```

**使用 Bower：**

```
bower install echojs
```

## 使用方法

在图片上添加 `data-echo` 属性填写图片的真实地址；如果想懒加载背景图片，可以使用 `data-echo-background' 属性填写真实地址。

```html
<img src="img/blank.gif" alt="Photo" data-echo="img/photo.jpg">

<script src="path/to/echo.js"></script>
<script>
echo.init({
  offset: 100,
  throttle: 250,
  unload: false,
  callback: function (element, op) {
    console.log(element, 'has been', op + 'ed')
  }
});

// echo.render(); is also available for non-scroll callbacks
</script>
```

## API 说明

### .init() (options)

`init()` API 有以下几个选项：

- **`offset`**

  类型：`Number|String`，默认： `0`

  `offset` 用于设置距离视口多远（水平、垂直方向）时开始载入图片， 为 `0` 时，图片进入视口以后立即加载。

- **`offsetVertical`**

  类型： `Number|String`，默认： `offset` 选项的值

  设置图片距离视口垂直方向上距离多少时开始载入图片。

- **`offsetHorizontal`**

  类型： `Number|String`，默认： `offset` 选项得值

  设置图片距离视口水平方向上距离多少时开始载入图片。

- **`offsetTop`**

  类型： `Number|String`，默认： `offsetVertical` 的值

  图片距离顶部多少时开始载入图片。

- **`offsetBottom`**

  类型：`Number|String`，默认：`offsetVertical` 的值

  图片距离底部多少时开始载入图片。

- **`offsetLeft`**

  类型： `Number|String`，默认：`offsetVertical` 的值

  图片距离左侧多少时开始载入图片

- **`offsetRight`**

  类型： `Number|String`，默认：`offsetVertical` 的值

  图片距离右侧多少时开始载入图片

- **`throttle`**

  类型：`Number|String`，默认：`250`

  控制 `window.onscroll` 触发频率，以避免过于频繁导致性能问题，默认为 `250` 毫秒。

- **`debounce`**

  类型： `Boolean`，默认：`true`

  [debounce](http://underscorejs.org/#debounce)，用户停止滚动时才触发位置检测函数。

- **`unload`**

  类型：`Boolean`，默认：`false`

  图片超过视口时不加载。

- **`callback`**

  类型： `Function`

  回调函数接受两个参数，第一个为当前元素，第二个为操作状态（如 `load`、`unload`）。


下面的代码会在图片加载完成后添加 `loaded` class。

```js
echo.init({
  callback: function(element, op) {
    if(op === 'load') {
      element.classList.add('loaded');
    } else {
      element.classList.remove('loaded');
    }
  }
});
```

### .render()

调用此方法可以在不滚动窗口的情况下触发图片加载。

```js
echo.render();
```

## License

MIT license

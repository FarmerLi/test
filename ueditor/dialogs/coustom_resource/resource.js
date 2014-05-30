/**
 * TAB切换
 * @param tabParentId  tab的父节点ID或者对象本身
 */
function switchTab(tabParentId) {
    var tabElements = $G(tabParentId).children,
        tabHeads = tabElements[0].children,
        tabBodys = tabElements[1].children;

    for (var i = 0, length = tabHeads.length; i < length; i++) {
        var head = tabHeads[i];
        if (head.className === "focus") {
        	clickHandler(tabHeads, tabBodys, head);
        }
        head.onclick = function () {
            clickHandler(tabHeads, tabBodys, this);
        }
    }
}

function clickHandler(tabHeads, tabBodys, obj)
{
	//head样式更改
    for (var k = 0, len = tabHeads.length; k < len; k++) {
        tabHeads[k].className = "";
    }
    obj.className = "focus";
    //body显隐
    var tabSrc = obj.getAttribute("tabSrc");
}
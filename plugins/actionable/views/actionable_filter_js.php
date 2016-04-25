var actionableStatus = [];
$.each($(".fl-actionable li a.selected"), function(i, item){
	statusVal = item.id.substring("filter_actionable_".length);
	actionableStatus.push(statusVal);
});
if (actionableStatus.length > 0)
{
	urlParameters["plugin_actionable_filter"] = actionableStatus;
}
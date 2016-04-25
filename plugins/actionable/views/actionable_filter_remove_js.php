if (elementId.indexOf('filter_actionable_') != -1)
{
	verification = elementId.substring('filter_actionable_'.length);
	removeParameterItem("plugin_actionable_filter", verification);
}
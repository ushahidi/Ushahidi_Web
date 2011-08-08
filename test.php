<form action="http://checkin.crdmp.com/api/" method="post" target="testiframe" enctype="multipart/form-data">
	task: <input type="text" name="task" value="checkin" /><br/>
	action: <input type="text" name="action" value="ci" /><br/>
	mobileid: <input type="text" name="mobileid" value="testingmobileid439234" /><br/>
	lat: <input type="text" name="lat" value="3" /><br/>
	lon: <input type="text" name="lon" value="4" /><br/>
	message: <input type="text" name="message" value="This is a test message" /><br/>
	photo: <input type="file" id="photo" name="photo" /><br/>
	<input type="submit" value="Do it!" />
</form>
<iframe name="testiframe" src="" width="100%" height="50%"></iframe>
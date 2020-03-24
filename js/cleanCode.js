// Funzione di pulizia * e * inizio e fine sparata
var cleanCode= function(code)
{
	if  (code.indexOf("*")>-1)
	{
		var n=code.trim().length;
		code=code.substring(1,n-1);
	}
	return code
}

var cleanObj= function (Obj)
{
	Obj.value=cleanCode(Obj.value);
};

	


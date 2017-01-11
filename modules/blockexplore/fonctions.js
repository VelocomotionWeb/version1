function Getheure()
{	
	H = new Date();
	heure   = H.getHours();
	minute  = H.getMinutes();
	seconde = H.getSeconds();

	if(eval(heure) < 10)
	{
		dh = "0";
		uh = heure;
	}
	else
	{
		heure += " ";
		dh = heure.charAt(0);
		uh = heure.charAt(1);
	}

	if(eval(minute) < 10)
	{
		dm = "0";
		um = minute;
	}
	else
	{
		minute += " ";
		dm = minute.charAt(0);
		um = minute.charAt(1);
	}
	
	if(eval(seconde) < 10)
	{
		ds = "0";
		us = seconde;
	}
	else
	{
		seconde += " ";
		ds = seconde.charAt(0);
		us = seconde.charAt(1);
	}

	document.DH.src = "./images/chiffre" + dh + ".gif";
	document.UH.src = "./images/chiffre" + uh + ".gif";
	document.DM.src = "./images/chiffre" + dm + ".gif";
	document.UM.src = "./images/chiffre" + um + ".gif";
	document.DS.src = "./images/chiffre" + ds + ".gif";
	document.US.src = "./images/chiffre" + us + ".gif";

	fonction = "Getheure()";

	ID = window.setTimeout(fonction,1000);
}
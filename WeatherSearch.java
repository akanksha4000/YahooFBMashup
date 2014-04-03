package com.usc.homework8;

import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;


import org.jdom.Document;
import org.jdom.input.SAXBuilder;
import org.jdom.output.XMLOutputter;
import org.json.JSONObject;
import org.json.XML;


/**
 * Servlet implementation class WeatherSearch
 */

public class WeatherSearch extends HttpServlet {
	private static final long serialVersionUID = 1L;

	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		try {
			//PrintWriter out=response.getWriter();
			
			//call to PHP
			
			String urlString="http://default-environment-ewkymjsam9.elasticbeanstalk.com/?"+request.getQueryString();
								
			URL url=new URL(urlString);
			URLConnection urlConnection = url.openConnection();
			urlConnection.setAllowUserInteraction(false);
			InputStream urlStream=url.openStream();
			//parse the XML
			SAXBuilder builder =new SAXBuilder();
			Document document = builder.build(urlStream);
			String xmlString = new XMLOutputter().outputString(document); //convert the XML to String
			JSONObject jsonObject  = XML.toJSONObject(xmlString); //convert the XML to JSONObject
			String jsonString = jsonObject.toString();
			System.out.println(jsonString);
			response.getWriter().println(jsonString);
		}
		catch (MalformedURLException e){
			
			response.getWriter().println("Oops! Something went wrong. Try after sometime!");
		}
		catch (IOException e){response.getWriter().println("Oops! Something went wrong. Try after sometime!");}
		catch (Exception e){response.getWriter().println("Oops! Something went wrong. Try after sometime!");}
	}

}

/* 
 * File:   ExternalFileHandler.h
 * Author: mato
 *
 * Created on Pondelok, 2013, december 9, 18:38
 * 
 * class ExternalFileHandler
 * subclass of FileHandler
 * handles exteral files - provides basic functionality for manipulating with external files
 */

#ifndef EXTERNALFILEHANDLER_H
#define	EXTERNALFILEHANDLER_H

#include <string>
#include <fstream>
#include <sstream>
#include <set>
#include <iostream>

#include "FileHandler.h"

using namespace std;

class ExternalFileHandler : public FileHandler {
    /* properties */
private:
    string      _path;
    string      _fileName;
    uint        _fileSize;
    uint        _dataType;
    bool        _valid;
    
    /*Constructors and destructors*/
public:
    ExternalFileHandler(string key,string path); //ok
    virtual ~ExternalFileHandler(); //ok
    
    /*Reimplemented abstract methods*/
public:
    virtual string getFileName(); //ok          //returns name of the file
    virtual unsigned int getDataType(); //ok    //returns data type of the file
    virtual unsigned int getFileLength(); //ok  //returns length of the file
    
    virtual void writeFileData(ofstream&); //ok
    virtual string getFilePath(); //ok
    
    /*Public interface*/
public:
    virtual bool isValid(); //ok
    
    /*Helpers*/
private:
    string strToLower(string&); //ok
    bool contains(string& str, string strList); //pk
    unsigned int getDataType(string filename); //ok
};

#endif	/* EXTERNALFILEHANDLER_H */


/* 
 * File:   FileHandler.h
 * Author: mato
 *
 * Created on Pondelok, 2013, december 9, 18:33
 * 
 * Change log:
 *  14/12/2013 - redefined interface, starting reimplementation of the class
 * 
 * * Fileheader
 * 
 *
 * class File Handler
 * this is abstract class. Base class for internal or external file handlers
 * handles basic file operations.
 *  
 * Main class operations:
 * 
 * --Header operations--
 * 
 *  - read file header: reads basic file information from package header,
 *                      external file, or other source, subclass specific
 *  - write file header: writes header information to byte array or file
 *  - get header information: returns information about file
 * 
 * 
 * --File operations--
 *  
 *  - write file data: writes file content to the output file stream or new file,
 *                     subclass specific
 *  - read file data: class provides user path to the file for futher processing,
 *                    subclass specific
 * 
 * Main class data:
 * 
 * --Header data--
 *  
 *  - key - id key of the file. This key serves as indentifier to quick search for file
 *          key is 8 character long string and has to be unique in package and chosen
 *          so file is uniquely indentified
 *  - file name - name of the file
 *  - file length - length of the file in bytes
 *  - data type - type of the data in the file, used to determine content of the file
 * 
 * --Header data structure--
 * 0 - 7:   (8B) Key
 * 8 - 11:  (4B) File length
 * 12 - 15: (4B) Data type
 * 16 - 19: (4B) Filename length
 * 20 - ?:  (?B) Filename string
 * 
 * --Header data length--
 * 8B + 4B + 4B + 4B + Filename length = 20B + Filename length
 */

#ifndef FILEHANDLER_H
#define	FILEHANDLER_H


#include <string>
#include <fstream>
#include "BitConverter.h"

typedef unsigned char uint8;

using namespace std;

class FileHandler {
public:
    /* Basic data types */
    enum{
        DATA_TYPE_UNKNOWN = 0,  //data type is unsupported by the package, 
                                //can be used to store custom user data
        DATA_TYPE_IMAGE = 1,    //basic image data, supported formats: jpg, gif, tiff, png, bmp
        DATA_TYPE_AUDIO = 2,    //basic audio data, supported formats: mp3, wav, ogg, raw
        DATA_TYPE_FONT = 3      //basic font data, supported formats: ttf
    };
    /*Properties*/
private:
    string _key;
    /* Constructors and destructors */
public:
    //default constructor
    FileHandler(); //ok
    //defaut destructor
    virtual ~FileHandler(); //ok
    
    /* Header operations */
 
    /* Header information - virtual functions to be subclassed
     * header information are class specific and depends on source of the file
     * Every subclass has to implement these functions
     */
public:  
    string getKey();                 //returns key of the file
    void setKey(string key);         //set the key
    virtual string getFileName() = 0;           //returns name of the file
    virtual unsigned int getDataType() = 0;     //returns data type of the file
    virtual unsigned int getFileLength() = 0;   //returns length of the file
    
    /**
     * Returns header size
     * @return size of header in bytes
     */
    unsigned int getHeaderSize(); //ok               //returns size of header in bytes
    
    /* Write header data - this function receives header information and writes them
     * to output file stream or byte array depending on parameters
     */
    
    /**
     * Writes file header into current position output file stream, 
     * data are writen in little endian system.
     * @param output file stream
     */
    void writeHeader(ofstream&); //ok 
    
    /**
     * Writes file header into byte array on specific position defined by offset.
     * Data are written in little endian system.
     * @param  byte_array - array to be written to
     *          offset - possition in the array where header will be written
     */
    void writeHeader(unsigned char* byte_array,int offset = 0); //ok
   
    /* File operations */
public:
    /**
     * writes file data to file defined by path
     * @param path - path of the file, where file data will be written
     */
    void writeFileData(string path);
    
    /**
     * writes file data to current position of output stream
     * @param output stream
     */
    virtual void writeFileData(ofstream&) = 0;
    
    /**
     * returns path to the file data, subclass specific
     * @return path to the source file
     */
    virtual string getFilePath() = 0;
    
    virtual bool isValid() = 0;
};

#endif	/* FILEHANDLER_H */

